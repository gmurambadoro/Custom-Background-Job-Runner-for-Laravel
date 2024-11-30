<x-layout>
    <h4>Add a new background job</h4>

    <div class="grid">
        <div>
            <form method="post" action="{{ route('background-jobs.store') }}">
                @csrf

                @if($errors->any())
                    <ul style="color: darkred;">
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif

                <fieldset>
                    <label>
                        Fully Qualified Class Name (FQCN) *
                        <input
                            name="fqcn"
                            placeholder="E.g. App\\Models\\User"
                            required
                            value="{{ old('fqcn', $job->fqcn) }}"
                        />
                    </label>
                    <label>
                        Method to call (do not include brackets) *
                        <input
                            type="text"
                            name="method"
                            placeholder="E.g. all"
                            required
                            value="{{ old('method', $job->method) }}"
                        />
                    </label>

                    <fieldset>
                        <label>
                            <input type="checkbox" value="{{ true }}"
                                   @checked(old('is_static', $job->is_static)) name="is_static"
                                   role="switch"/>

                            <span>The method is called <samp>statically</samp> on the <samp>FQCN</samp></span>
                        </label>
                    </fieldset>

                    <label>
                        Arguments to pass to the method (each argument must be on its)

                        <textarea
                            name="arguments"
                            placeholder="Multiple arguments must be passed one per line">{{ old('arguments', $job->arguments) }}</textarea>
                    </label>
                </fieldset>

                <fieldset>
                    <label>
                        Job Priority *
                        <select name="priority">
                            @foreach(\App\Enums\JobPriorityEnum::options() as $name => $value)
                                <option
                                    value="{{ $value }}" @selected($value === $job->priority->value)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </label>
                </fieldset>

                <fieldset>
                    <label>
                        Job Delay (in seconds) *

                        <input type="number" min="0" value="{{ old('delay', $job->delay) }}" name="delay"/>
                    </label>
                </fieldset>

                <div class="grid">
                    <div>
                        <a href="{{ route('background-jobs.index') }}" role="button" class="secondary">
                            Cancel
                        </a>
                    </div>

                    <div>
                        <input
                            type="submit"
                            value="Schedule as background job"
                        />
                    </div>
                </div>
            </form>
        </div>
        <div></div>
    </div>
</x-layout>
