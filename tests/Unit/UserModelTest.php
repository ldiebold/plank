<?php

namespace App\Models {
    use Illuminate\Database\Eloquent\Model;

    class Challenge extends Model {}

    class PlankCompletion extends Model {}
}

namespace {
    use App\Models\User;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    it('fills countdown seconds on the user model', function () {
        expect((new User)->getFillable())->toContain('countdown_seconds');
    });

    it('defines the expected relationship return types', function () {
        $user = new User;

        expect($user->challenges())->toBeInstanceOf(BelongsToMany::class);
        expect($user->plankCompletions())->toBeInstanceOf(HasMany::class);
    });

    it('currentChallenge returns null when not in challenge', function () {
        $user = new User;
        expect($user->currentChallenge())->toBeNull();
    });

    it('reports whether the user is in challenge', function () {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('challenges->exists')->once()->andReturn(true);

        expect($user->isInChallenge())->toBeTrue();
    });
}
