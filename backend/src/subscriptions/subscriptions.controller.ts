import { Body, Controller, Get, Post, UseGuards } from '@nestjs/common';
import { AuthUser } from '../auth/auth-user';
import { CurrentUser } from '../auth/current-user.decorator';
import { JwtAuthGuard } from '../auth/jwt-auth.guard';
import { CreateSubscriptionDto } from './dto/create-subscription.dto';
import { SubscriptionsService } from './subscriptions.service';

@UseGuards(JwtAuthGuard)
@Controller('subscriptions')
export class SubscriptionsController {
  constructor(private readonly subscriptionsService: SubscriptionsService) {}

  @Get()
  findAll(@CurrentUser() user: AuthUser) {
    return this.subscriptionsService.findAll(user);
  }

  @Post()
  create(@CurrentUser() user: AuthUser, @Body() dto: CreateSubscriptionDto) {
    return this.subscriptionsService.create(user, dto);
  }
}
