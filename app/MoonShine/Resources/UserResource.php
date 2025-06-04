<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use Illuminate\Validation\Rule;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

#[Icon('users')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Users';

    protected bool $columnSelection = true;

    protected bool $simplePaginate = true;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make(__('moonshine::ui.resource.name'), 'name'),
            Email::make(__('moonshine::ui.resource.email'), 'email')
                ->sortable(),
            Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                ->format("d.m.Y")
                ->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Grid::make([
                Column::make([
                    Box::make(__('moonshine::ui.clients.contact_information'), [
                        ID::make()->sortable(),
                        Text::make(__('moonshine::ui.clients.name'), 'name')
                            ->required()
                            ->customAttributes(['autocomplete' => 'name'])
                            ->placeholder(__('moonshine::ui.resource.name')),
                        Email::make(__('moonshine::ui.clients.email'), 'email'),
                    ]),
                    LineBreak::make(),
                    Box::make(__('moonshine::ui.clients.password_title'), [
                        Password::make(__('moonshine::ui.clients.password'))
                            ->customAttributes(['autocomplete' => 'new-password']),
                        PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'))
                            ->customAttributes(['autocomplete' => 'confirm-password']),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required',
            'email' => [
                'sometimes',
                'bail',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($item->id),
            ],
            'password' => !$item->exists
                ? 'required|min:6|required_with:password_repeat|same:password_repeat'
                : 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return __('moonshine::ui.resource.client_title');
    }

    protected function filters(): iterable
    {
        return [
            Text::make('E-mail', 'email')
                ->onApply(fn(Builder $query, ?string $value) => $value === null ? $query : $query->whereLike('email', "%$value%")),
        ];
    }
}
