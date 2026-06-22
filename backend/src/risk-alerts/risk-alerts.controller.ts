import { Body, Controller, Get, Param, Patch, UseGuards } from '@nestjs/common';
import { AuthUser } from '../auth/auth-user';
import { CurrentUser } from '../auth/current-user.decorator';
import { JwtAuthGuard } from '../auth/jwt-auth.guard';
import { UpdateRiskAlertDto } from './dto/update-risk-alert.dto';
import { RiskAlertsService } from './risk-alerts.service';

@UseGuards(JwtAuthGuard)
@Controller('risk-alerts')
export class RiskAlertsController {
  constructor(private readonly riskAlertsService: RiskAlertsService) {}

  @Get()
  findAll(@CurrentUser() user: AuthUser) {
    return this.riskAlertsService.findAll(user);
  }

  @Patch(':id')
  update(
    @CurrentUser() user: AuthUser,
    @Param('id') id: string,
    @Body() dto: UpdateRiskAlertDto,
  ) {
    return this.riskAlertsService.update(user, id, dto);
  }
}
