import { RiskAlertStatus } from '@prisma/client';

export interface UpdateRiskAlertDto {
  status?: RiskAlertStatus;
  resolvedAt?: string | null;
}
