@if ($values->isNotEmpty())
    <div class="d-flex justify-content-between">
        <p class="font-weight-light text-secondary mt-4">{{ __('a.shown') . $values->count() . __('a.of') .  $values->total() }}</p>
        <div class="mt-3">{{ $values
                    ->appends([
                        'col' => s(request()->query('col')),
                        'cell' => s(request()->query('cell')),
                        ])
                    ->onEachSide(2)
                    ->links() }}</div>
    </div>
@endif
