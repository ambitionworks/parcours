<x-popup-notification :data="$data" :link="route('user.profile', $data['user']['slug'])">
    <strong>{{ $data['user']['name'] }}</strong> {{ __('requested to follow you') }}.
</x-popup-notification>