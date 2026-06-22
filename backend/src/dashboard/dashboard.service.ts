import { ForbiddenException, Injectable } from '@nestjs/common';
import {
  LeadStatus,
  MemberStatus,
  PaymentStatus,
  ReservationStatus,
  RiskAlertStatus,
  RoleKey,
  SubscriptionStatus,
  TaskStatus,
} from '@prisma/client';
import { AuthUser } from '../auth/auth-user';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class DashboardService {
  constructor(private readonly prisma: PrismaService) {}

  async getSummary(user: AuthUser) {
    const tenantId = this.requireTenant(user);
    const now = new Date();
    const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
    const weekStart = this.getWeekStart(now);

    const [
      activeMembers,
      openLeads,
      newMembersThisMonth,
      pendingPayments,
      overduePayments,
      overdueTasks,
      upcomingReservations,
      weeklyCheckIns,
      openAlerts,
      activeSubscriptions,
      recentLeads,
      upcomingTasks,
      openRiskAlerts,
    ] = await this.prisma.$transaction([
      this.prisma.member.count({
        where: { tenantId, status: MemberStatus.ACTIVE },
      }),
      this.prisma.lead.count({
        where: { tenantId, status: LeadStatus.OPEN },
      }),
      this.prisma.member.count({
        where: {
          tenantId,
          joinedAt: { gte: monthStart },
        },
      }),
      this.prisma.payment.count({
        where: { tenantId, status: PaymentStatus.PENDING },
      }),
      this.prisma.payment.count({
        where: { tenantId, status: PaymentStatus.OVERDUE },
      }),
      this.prisma.task.count({
        where: {
          tenantId,
          status: TaskStatus.PENDING,
          dueAt: { lt: now },
        },
      }),
      this.prisma.reservation.count({
        where: {
          tenantId,
          status: ReservationStatus.RESERVED,
          classSession: {
            startsAt: { gte: now },
          },
        },
      }),
      this.prisma.checkIn.count({
        where: {
          tenantId,
          checkedInAt: { gte: weekStart },
        },
      }),
      this.prisma.riskAlert.count({
        where: { tenantId, status: RiskAlertStatus.OPEN },
      }),
      this.prisma.subscription.findMany({
        where: {
          tenantId,
          status: SubscriptionStatus.ACTIVE,
        },
        include: {
          membershipPlan: {
            select: {
              price: true,
            },
          },
        },
      }),
      this.prisma.lead.findMany({
        where: { tenantId },
        include: {
          pipelineStage: true,
          assignedUser: {
            select: {
              id: true,
              name: true,
              email: true,
            },
          },
        },
        orderBy: { createdAt: 'desc' },
        take: 5,
      }),
      this.prisma.task.findMany({
        where: {
          tenantId,
          status: TaskStatus.PENDING,
        },
        include: {
          assignedUser: {
            select: {
              id: true,
              name: true,
              email: true,
            },
          },
          lead: {
            select: {
              id: true,
              firstName: true,
              lastName: true,
            },
          },
          member: {
            select: {
              id: true,
              firstName: true,
              lastName: true,
            },
          },
        },
        orderBy: [{ dueAt: 'asc' }, { createdAt: 'desc' }],
        take: 5,
      }),
      this.prisma.riskAlert.findMany({
        where: {
          tenantId,
          status: RiskAlertStatus.OPEN,
        },
        include: {
          lead: {
            select: {
              id: true,
              firstName: true,
              lastName: true,
            },
          },
          member: {
            select: {
              id: true,
              firstName: true,
              lastName: true,
            },
          },
          task: {
            select: {
              id: true,
              title: true,
            },
          },
        },
        orderBy: [{ severity: 'desc' }, { detectedAt: 'desc' }],
        take: 5,
      }),
    ]);

    const estimatedMrr = activeSubscriptions.reduce((total, subscription) => {
      return total + Number(subscription.membershipPlan.price);
    }, 0);

    return {
      generatedAt: now,
      kpis: {
        activeMembers,
        openLeads,
        newMembersThisMonth,
        pendingPayments,
        overduePayments,
        overdueTasks,
        upcomingReservations,
        weeklyCheckIns,
        openAlerts,
        estimatedMrr: Number(estimatedMrr.toFixed(2)),
      },
      recentLeads,
      upcomingTasks,
      openRiskAlerts,
    };
  }

  private requireTenant(user: AuthUser): string {
    if (!user.tenantId || user.role === RoleKey.SUPERADMIN) {
      throw new ForbiddenException('A tenant user is required');
    }

    return user.tenantId;
  }

  private getWeekStart(date: Date) {
    const result = new Date(date);
    const day = result.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    result.setDate(result.getDate() + diff);
    result.setHours(0, 0, 0, 0);
    return result;
  }
}
