<?php

namespace App\Preferences;


use Illuminate\Database\Eloquent\Collection;

/*
 * |-----------------------------------------------------------------------------
 * | Preference System
 * |-----------------------------------------------------------------------------
 * | The preference system works by simply storing preference data as key-value
 * | pairs. There can be multiple values per one name, this means it is an array
 * | of preferences. This system has its restrictions, but it does its job on
 * | the scale of this application, so there is no need to store validation
 * | data on a separate table, what possible choices it has, etc.
 * 
 * todo: use one preference table and model for all models that use preferences
 * todo: implement a system similar to notifications in base Laravel
 */
trait HasPreferences
{
    protected array $preferencesDefaults;

    /**
     * Get preference value. Returns either a string value,
     * or an array of the preference choices.
     *
     * @param string $input
     * @return string|array
     */
    public function preference(string $input): string|array
    {
        $preference = $this->toNormal($this->preferences->where('name', $input)->get());
        if ($preference == null)
            $preference = $this->preferencesDefaults[$input];
        return $preference;
    }

    public function setPreference(string $name, string|array $value): void
    {
        if ($this->preferencesDefaults[$name] === $value) {
            $this->resetPreference($name);
            return;
        }

        $preference = $this->toNormal($this->preferences->where('name', $name)->get());
        if ($preference == null) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $this->preferences()->create(['name' => $name, 'value' => $item]);
                }
            } else {
                $this->preferences()->create(['name' => $name, 'value' => $value]);
            }
        }

        if (is_array($preference)) {
            $this->resetPreference($name);
            if (is_array($value))
                foreach ($value as $item) {
                    $this->preferences()->create(['name' => $name, 'value' => $item]);
                }
            else
                $this->preferences()->create(['name' => $name, 'value' => $value]);
            return;
        }

        
    }

    public function appendPreference(string $name, string $value): void
    {

    }

    public function resetPreference(string $name): void
    {
        if (!$this->preferences->where('name', $name)->exists() == 0) {
            return;
        }

        $this->preferences->where('name', $name)->delete();
    }

    /**
     * Turn a collection of preferences with the same name into a form that can
     * be returned. For single-value preferences it returns a string, for
     * multiple choice preferences it returns an array of strings.
     *
     * @param Collection $collection
     * @return string|array|null
     */
    private function toNormal(Collection $collection): null|string|array
    {
        if ($collection->count() == 0) {
            return null;
        }

        if ($collection->count() == 1) {
            return $collection->first()->value;
        }

        return $collection->map(function ($value) {
            return $value->value;
        })->toArray();
    }
}