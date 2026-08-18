@extends('admin.layout.master')
@section('content')
    <div class="container mb-2 fw-bold">
        @if (session('deleteSuccess'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('deleteSuccess') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- Success Message Alert --}}
        @if (session('addSuccess'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('addSuccess') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="mb-2 col-12 col-md-6 col-lg-5">
                <div class="bg-white border shadow-lg border-3 border-primary bg-opacity-10 text-dark card auth-card"
                    style="backdrop-filter: blur(6.5px); border-radius: 20px;">
                    <div class="p-4 card-body">
                        <div class="pb-3 mb-4 row align-items-center">
                            <!-- Title -->
                            <div class="text-center col-md-6 text-md-start text-nowrap">
                                <h3 class="mb-0 fw-bold">{{ __('Add Librarian') }}</h3>
                            </div>

                            <!-- Profile Image Preview -->
                            <div class="mt-1 text-center col-md-6 text-md-end">
                                <img id="output" src="" class="shadow rounded-circle">
                            </div>

                        </div>
                        <form method="POST" action="{{ route('admin.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Name -->
                                <div class="mb-3 col-md-12">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}">

                                    @error('name')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email -->
                                <div class="mb-3 col-md-12">
                                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                    <input id="email" type="text"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" autocomplete="username">

                                    @error('email')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Password -->
                                <div class="mb-3 col-md-12">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password">

                                    @error('password')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3 col-md-12">
                                    <label for="password_confirmation"
                                        class="form-label">{{ __('Confirm Password') }}</label>
                                    <input id="password_confirmation" type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" autocomplete="new-password">

                                    @error('password_confirmation')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Profile Image -->
                                <div class="mb-3 col-md-12">
                                    <label for="profile_image" class="form-label">{{ __('Profile Image') }}</label>
                                    <input id="profile_image" type="file" onchange="loadFile(event)"
                                        class="form-control @error('profile_image') is-invalid @enderror"
                                        name="profile_image" accept="image/*">

                                    @error('profile_image')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-7">
                <div class="bg-white border shadow-lg border-3 border-secondary bg-opacity-10 text-dark card"
                    style="border-radius: 20px;">
                    <div class="p-4 card-body">
                        <h4 class="mb-3 fw-bold">{{ __('Librarian List') }}</h4>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>{{ __('Profile') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th class="text-center">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($librarians)
                                        @forelse ($librarians as $librarian)
                                            <tr>
                                                <td>
                                                    @if ($librarian->profile)
                                                        <img src="{{ asset('userProfile/' . $librarian->profile) }}"
                                                            class="rounded-circle"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('default-avatar.png') }}" class="rounded-circle"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                </td>
                                                <td>{{ $librarian->name }}</td>
                                                <td>{{ $librarian->email }}</td>
                                                <td class="text-center">
                                                    {{-- Bootstrap Modal ကိုခေါ်မည့် Delete Button --}}
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                        onclick="setDeleteUrl('{{ route('admin.delete', $librarian->id) }}')">
                                                        <i class="fa-solid fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-4 text-center text-muted">
                                                    {{ __('No Librarians Found') }}</td>
                                            </tr>
                                        @endforelse
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="border-0 modal-header">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">{{ __('Confirm Delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="py-4 text-center modal-body">
                    <i class="mb-3 fa-solid fa-triangle-exclamation text-warning fs-1"></i>
                    <p class="mb-0 fs-5">{{ __('Are you sure you want to delete this librarian?') }}</p>
                </div>
                <div class="pb-4 border-0 modal-footer justify-content-center">
                    <button type="button" class="px-4 btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 btn btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    // Modal ထဲသို့ သက်ဆိုင်ရာ Delete Route ကို ထည့်ပေးရန် Function
    function setDeleteUrl(url) {
        let deleteForm = document.getElementById('deleteForm');
        deleteForm.action = url;
    }

    function loadImage(event) {
        let output = document.getElementById('output');
        output.style.width = "300px";
        output.style.height = "600px";
        output.style.objectFit = "cover";
        var reader = new FileReader();

        reader.onload = function() {
            output.src = reader.result
        }
        reader.readAsDataURL(event.target.files[0])
    }

    function loadFile(event) {
        let output = document.getElementById('output');
        output.style.width = "100px";
        output.style.height = "100px";
        output.style.objectFit = "cover";
        var reader = new FileReader();

        reader.onload = function() {
            output.src = reader.result
        }
        reader.readAsDataURL(event.target.files[0])
    }
</script>
@endsection