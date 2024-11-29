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
                        />
                    </label>
                    <label>
                        Method to call (do not include brackets) *
                        <input
                            type="text"
                            name="method"
                            placeholder="E.g. all"
                            required
                        />
                    </label>

                    <fieldset>
                        <label>
                            <input type="checkbox" value="{{ true }}" name="is_static" role="switch"/>

                            <span>The method is called <samp>statically</samp> on the <samp>FQCN</samp></span>
                        </label>
                    </fieldset>

                    <label>
                        Arguments to pass to the method (each argument must be on its)

                        <textarea name="arguments"
                                  placeholder="Multiple arguments must be passed one per line"></textarea>
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
