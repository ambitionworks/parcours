<x-popup-notification :data="$data" :link="route('activities.show', $data['activity']['id'])">
    <strong>{{ $data['user']['name'] }}</strong> {{ __('liked your activity') }} <em>{{ $data['activity']['name'] }}</em>.
</x-popup-notification>