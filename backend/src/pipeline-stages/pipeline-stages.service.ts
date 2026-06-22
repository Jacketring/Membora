import { ForbiddenException, Injectable } from '@nestjs/common';
import { RoleKey } from '@prisma/client';
import { AuthUser } from '../auth/auth-user';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class PipelineStagesService {
  constructor(private readonly prisma: PrismaService) {}

  findAll(user: AuthUser) {
    const tenantId = this.requireTenant(user);

    return this.prisma.pipelineStage.findMany({
      where: { tenantId },
      select: {
        id: true,
        name: true,
        key: true,
        order: true,
        isFinal: true,
      },
      orderBy: { order: 'asc' },
    });
  }

  private requireTenant(user: AuthUser): string {
    if (!user.tenantId || user.role === RoleKey.SUPERADMIN) {
      throw new ForbiddenException('A tenant user is required');
    }

    return user.tenantId;
  }
}
