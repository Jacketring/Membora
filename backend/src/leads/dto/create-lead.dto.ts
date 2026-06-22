import { LeadSource } from '@prisma/client';

export interface CreateLeadDto {
  pipelineStageId: string;
  assignedUserId?: string;
  firstName: string;
  lastName?: string;
  email?: string;
  phone?: string;
  source?: LeadSource;
  interest?: string;
  nextActionAt?: string;
}
