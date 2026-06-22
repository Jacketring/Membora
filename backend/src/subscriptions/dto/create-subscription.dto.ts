import { SubscriptionStatus } from '@prisma/client';

export interface CreateSubscriptionDto {
  memberId: string;
  membershipPlanId: string;
  status?: SubscriptionStatus;
  startDate?: string;
  endDate?: string | null;
}
