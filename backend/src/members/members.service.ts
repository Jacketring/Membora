import { ForbiddenException, Injectable, NotFoundException } from '@nestjs/common';
import { RoleKey } from '@prisma/client';
import { AuthUser } from '../auth/auth-user';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class MembersService {
  constructor(private readonly prisma: PrismaService) {}

  findAll(user: AuthUser) {
    const tenantId = this.requireTenant(user);

    return this.prisma.member.findMany({
      where: { tenantId },
      include: {
        lead: {
          select: {
            id: true,
            firstName: true,
            lastName: true,
            email: true,
            status: true,
          },
        },
        subscriptions: {
          include: {
            membershipPlan: true,
          },
          orderBy: { createdAt: 'desc' },
        },
      },
      orderBy: { createdAt: 'desc' },
    });
  }

  async findOne(user: AuthUser, id: string) {
    const tenantId = this.requireTenant(user);
    const member = await this.prisma.member.findFirst({
      where: { id, tenantId },
      include: {
        lead: true,
        subscriptions: {
          include: {
            membershipPlan: true,
            payments: true,
          },
          orderBy: { createdAt: 'desc' },
        },
        payments: {
          orderBy: { createdAt: 'desc' },
        },
        reservations: {
          include: {
            classSession: {
              include: {
                classType: true,
                trainer: {
                  select: {
                    id: true,
                    name: true,
                    email: true,
                  },
                },
              },
            },
          },
          orderBy: { createdAt: 'desc' },
        },
        checkIns: {
          orderBy: { checkedInAt: 'desc' },
        },
        tasks: {
          orderBy: { createdAt: 'desc' },
        },
        communicationLogs: {
          orderBy: { occurredAt: 'desc' },
        },
        riskAlerts: {
          orderBy: { detectedAt: 'desc' },
        },
      },
    });

    if (!member) {
      throw new NotFoundException('Member not found');
    }

    return member;
  }

  private requireTenant(user: AuthUser): string {
    if (!user.tenantId || user.role === RoleKey.SUPERADMIN) {
      throw new ForbiddenException('A tenant user is required');
    }

    return user.tenantId;
  }
}
