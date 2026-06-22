import {
  BadRequestException,
  ForbiddenException,
  Injectable,
  NotFoundException,
} from '@nestjs/common';
import { RoleKey, SubscriptionStatus } from '@prisma/client';
import { AuthUser } from '../auth/auth-user';
import { PrismaService } from '../prisma/prisma.service';
import { CreateSubscriptionDto } from './dto/create-subscription.dto';

@Injectable()
export class SubscriptionsService {
  constructor(private readonly prisma: PrismaService) {}

  findAll(user: AuthUser) {
    const tenantId = this.requireTenant(user);

    return this.prisma.subscription.findMany({
      where: { tenantId },
      include: {
        member: {
          select: {
            id: true,
            firstName: true,
            lastName: true,
            email: true,
            status: true,
          },
        },
        membershipPlan: true,
        payments: {
          orderBy: { createdAt: 'desc' },
        },
      },
      orderBy: { createdAt: 'desc' },
    });
  }

  async create(user: AuthUser, dto: CreateSubscriptionDto) {
    const tenantId = this.requireTenant(user);

    if (!dto.memberId || !dto.membershipPlanId) {
      throw new BadRequestException('memberId and membershipPlanId are required');
    }

    const member = await this.prisma.member.findFirst({
      where: { id: dto.memberId, tenantId },
      select: { id: true },
    });

    if (!member) {
      throw new NotFoundException('Member not found');
    }

    const plan = await this.prisma.membershipPlan.findFirst({
      where: { id: dto.membershipPlanId, tenantId, isActive: true },
      select: {
        id: true,
        durationDays: true,
      },
    });

    if (!plan) {
      throw new BadRequestException('Invalid membershipPlanId');
    }

    const activeSubscription = await this.prisma.subscription.findFirst({
      where: {
        tenantId,
        memberId: dto.memberId,
        status: SubscriptionStatus.ACTIVE,
      },
      select: { id: true },
    });

    if (activeSubscription && dto.status !== SubscriptionStatus.CANCELLED) {
      throw new BadRequestException('Member already has an active subscription');
    }

    const startDate = this.parseDate(dto.startDate) ?? new Date();
    const endDate =
      this.parseNullableDate(dto.endDate) ??
      this.calculateEndDate(startDate, plan.durationDays);

    return this.prisma.subscription.create({
      data: {
        tenantId,
        memberId: dto.memberId,
        membershipPlanId: dto.membershipPlanId,
        status: dto.status ?? SubscriptionStatus.ACTIVE,
        startDate,
        endDate,
      },
      include: {
        member: {
          select: {
            id: true,
            firstName: true,
            lastName: true,
            email: true,
          },
        },
        membershipPlan: true,
      },
    });
  }

  private requireTenant(user: AuthUser): string {
    if (!user.tenantId || user.role === RoleKey.SUPERADMIN) {
      throw new ForbiddenException('A tenant user is required');
    }

    return user.tenantId;
  }

  private parseDate(value?: string): Date | undefined {
    if (!value) {
      return undefined;
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      throw new BadRequestException('Invalid date');
    }

    return date;
  }

  private parseNullableDate(value?: string | null): Date | null | undefined {
    if (value === undefined) {
      return undefined;
    }

    if (value === null || value.trim() === '') {
      return null;
    }

    return this.parseDate(value);
  }

  private calculateEndDate(startDate: Date, durationDays?: number | null) {
    if (!durationDays) {
      return null;
    }

    const endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + durationDays);
    return endDate;
  }
}
