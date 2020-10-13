@props(['user' => null, 'userId' => null, 'size' => 'normal'])

@php
    switch ($size) {
        case 'large':
            $class = 'h-12 w-12 rounded-full';
            break;

        case 'normal':
        default:
            $class = 'h-8 w-8 rounded-full';
            break;
    }

    if (!$user && !$userId) {
        throw new \Exception('Problem calling <x-user-profile-photo>: Missing data');
    } elseif (!$user && $userId) {
        $user = \App\Models\User::find($userId);
    }
@endphp

<img {{ $attributes->merge(['class' => $class]) }} src="{{ $user->profile_photo_url }}" alt="" />