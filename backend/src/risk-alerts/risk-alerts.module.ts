import { Module } from '@nestjs/common';
import { AuthModule } from '../auth/auth.module';
import { PrismaModule } from '../prisma/prisma.module';
import { RiskAlertsController } from './risk-alerts.controller';
import { RiskAlertsService } from './risk-alerts.service';

@Module({
  imports: [AuthModule, PrismaModule],
  controllers: [RiskAlertsController],
  providers: [RiskAlertsService],
})
export class RiskAlertsModule {}
