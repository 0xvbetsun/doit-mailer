@component('mail::message')
    <div>
        <h4 class="media-heading">Hi friend!</h4>
        <p>{{ $message }}</p>
        <p>The weather in {{ $location }} looks like that now?</p>
        {{ Html::image('images/weather/' . $weatherImage . '.png', 'something went wrong with weather image :(', ['class' => 'img-thumbnail']) }}

    </div>

@endcomponent
