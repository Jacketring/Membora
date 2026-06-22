import { Controller, Get, UseGuards } from '@nestjs/common';
import { AuthUser } from '../auth/auth-user';
import { CurrentUser } from '../auth/current-user.decorator';
import { JwtAuthGuard } from '../auth/jwt-auth.guard';
import { PipelineStagesService } from './pipeline-stages.service';

@UseGuards(JwtAuthGuard)
@Controller('pipeline-stages')
export class PipelineStagesController {
  constructor(private readonly pipelineStagesService: PipelineStagesService) {}

  @Get()
  findAll(@CurrentUser() user: AuthUser) {
    return this.pipelineStagesService.findAll(user);
  }
}
