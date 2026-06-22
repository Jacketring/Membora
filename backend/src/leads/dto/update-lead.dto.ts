import { LeadSource, LeadStatus } from '@prisma/client';

export interface UpdateLeadDto {
  pipelineStageId?: string;
  assignedUserId?: string | null;
  firstName?: string;
  lastName?: string | null;
  email?: string | null;
  phone?: string | null;
  source?: LeadSource;
  interest?: string | null;
  status?: LeadStatus;
  lostReason?: string | null;
  nextActionAt?: string | null;
}
