import { TaskStatus, TaskType } from '@prisma/client';

export interface UpdateTaskDto {
  assignedUserId?: string | null;
  leadId?: string | null;
  memberId?: string | null;
  memberIds?: string[];
  title?: string;
  description?: string | null;
  type?: TaskType;
  status?: TaskStatus;
  dueAt?: string | null;
  completedAt?: string | null;
}
