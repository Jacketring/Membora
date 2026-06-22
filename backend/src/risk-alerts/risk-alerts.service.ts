import {
  BadRequestException,
  ForbiddenException,
  Injectable,
  NotFoundException,
} from '@nestjs/common';
import { RiskAlertStatus, RoleKey } from '@prisma/client';
import { AuthUser } from '../auth/auth-user';
import { PrismaService } from '../prisma/prisma.service';
import { UpdateRiskAlertDto } from './dto/update-risk-alert.dto';

@Injectable()
export class RiskAlertsService {
  constructor(private readonly prisma: PrismaService) {}

  findAll(user: AuthUser) {
    const tenantId = this.requireTenant(user);

    return this.prisma.riskAlert.findMany({
      where: { tenantId },
      include: {
        lead: {
          select: {
            id: true,
            firstName: true,
            lastName: true,
            email: true,
          },
        },
        member: {
          select: {
            id: true,
            firstName: true,
            lastName: true,
            email: true,
          },
        },
        task: {
          select: {
            id: true,
            title: true,
            status: true,
            dueAt: true,
          },
        },
      },
      orderBy: [{ status: 'asc' }, { severity: 'desc' }, { detectedAt: 'desc' }],
    });
  }

  async update(user: AuthUser, id: string, dto: UpdateRiskAlertDto) {
    const tenantId = this.requireTenant(user);
    const alert = await this.prisma.riskAlert.findFirst({
      where: { id, tenantId },
      select: { id: true },
    });

    if (!alert) {
      throw new NotFoundException('Risk alert not found');
    }

    const status = dto.status;
    const resolvedAt =
      dto.resolvedAt === undefined
        ? status === RiskAlertStatus.RESOLVED
          ? new Date()
          : undefined
        : this.parseNullableDate(dto.resolvedAt);

    return this.prisma.riskAlert.update({
      where: { id },
      data: {
        status,
        resolvedAt,
      },
    });
  }

  private requireTenant(user: AuthUser): string {
    if (!user.tenantId || user.role === RoleKey.SUPERADMIN) {
      throw new ForbiddenException('A tenant user is required');
    }

    return user.tenantId;
  }

  private parseNullableDate(value?: string | null): Date | null | undefined {
    if (value === undefined) {
      return undefined;
    }

    if (value === null || value.trim() === '') {
      return null;
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      throw new BadRequestException('Invalid date');
    }

    return date;
  }
}
