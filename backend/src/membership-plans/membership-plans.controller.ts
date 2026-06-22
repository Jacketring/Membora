import { Controller, Get, UseGuards } from '@nestjs/common';
import { AuthUser } from '../auth/auth-user';
import { CurrentUser } from '../auth/current-user.decorator';
import { JwtAuthGuard } from '../auth/jwt-auth.guard';
import { MembershipPlansService } from './membership-plans.service';

@UseGuards(JwtAuthGuard)
@Controller('membership-plans')
export class MembershipPlansController {
  constructor(private readonly membershipPlansService: MembershipPlansService) {}

  @Get()
  findAll(@CurrentUser() user: AuthUser) {
    return this.membershipPlansService.findAll(user);
  }
}
