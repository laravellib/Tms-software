<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

class SocialLoginControllerTest extends UnitTestCase
{

    public function testSocialLogin()
    {
        $user = factory(\App\Entities\User::class)->create();

        $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app->instance('Laravel\Socialite\Contracts\Factory', $mockSocialite);
        $mockSocialDriver = \Mockery::mock('Laravel\Socialite\Contracts\Provider');

        $mockSocialite->shouldReceive('driver')->twice()->with('google')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->once()->andReturn(redirect('/'));
        $mockSocialDriver->shouldReceive('user')->once()->andReturn($user);

        $this->visit('/auth/social/google');
        $this->visit('/auth/google/callback');

        $this->seeInDatabase('users', ['name' => $user->name, 'email' => $user->email]);
    }
}
