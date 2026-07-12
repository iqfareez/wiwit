{{-- Tailwind styling not working here. Because this component in rendered inside filament, 
and they only contains subset of tailwind classes. 
Though we can setup theme https://filamentphp.com/docs/4.x/styling/overview#setting-up-tailwind-css-for-your-project,
I don't use them because only one component only --}}
@if (filled(config('app.version')))
    <div
        style="width: 100%; padding: 1rem 1.5rem; color: var(--gray-400); font-size: 14px; line-height: 20px; text-align: center;">
        Ver. {{ config('app.version') }}
    </div>
@endif
