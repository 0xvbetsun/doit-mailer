@component('mail::message')
    <div>
        <h4 class="media-heading">Hi friend!</h4>
        <p>{{ $message }}</p>
        <p>The weather now in {{ $location }} looks like that?</p>
        {{ Html::image('images/weather/' . $weatherImage . '.png', 'something went wrong with weather image :(', ['class' => 'img-thumbnail']) }}

    </div>

@endcomponent
