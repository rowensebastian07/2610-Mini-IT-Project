@php
    $themes = config('themes');
@endphp


<x-top-nav>
</x-top-nav>

<!DOCTYPE html>
<html>
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Clubs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/create-clubs.css') }}">
</head>

<header>
    <img src="{{ asset('images/csrw-placeholder-2.jpeg') }}" 
         style="width:100%; height:200px; object-fit:cover;">
    <h1>Want to Introduce a new club to MMU?</h1>
   </header>


<body>
<div >
    <h2 id = "create-club-h2">Create Club</h2>
    <p>Fill in the details below</p>

@if ($errors->any())
    <div style="background:#fee;border:1px solid red;padding:10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="create-club-form">
    <form action="{{ route('create-clubs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group-clubs">
            <label for="name">Name</label><br>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group-clubs">
            <label for="description">Description</label><br>
            <input name="description" id="description"></input>
        </div>

        <div class="form-group-clubs div_pic">
            <label for="profile_picture">Profile Picture</label><br>
            <label id="pic_label">
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </label>
        </div>

        <div class="form-group">
                <label for="banner_image">Banner Image</label><br>
                <label id="banner_label">
                <input type="file" name="banner_image" id="banner_image" accept="image/*">
                </label>
            </div>

        <div class="form-group-clubs">
            <label for="category">Category</label><br>
            <select name="category" id="category">
                <option value="Arts Clubs">Art Clubs</option>
                <option value="Community Clubs">Community Clubs</option>
                <option value="Religious Clubs">Religious Clubs</option>
                <option value="Games / Entertainment Clubs">Games / Entertainment Clubs</option>
                <option value="Cultural Clubs">Cultural Clubs</option>
                <option value="Tech Clubs">Tech Clubs</option>
                <option value="Recreational / Physical Activities Clubs">Recreational / Physical Activities Clubs</option>
            </select>
        </div>

        <div class="form-group-clubs">
                <label for="theme">Theme</label><br>
                <select name="theme" id="theme">
                     @foreach($themes as $themeName => $theme)
                        <option value="{{ $themeName }}"
                            {{ $club->theme == $themeName ? 'selected' : '' }}>
                            {{ $themeName }}
                        </option>
                    @endforeach
                </select>
            </div>

        <button type="submit" class="btn-submit-clubs">Create Club</button>
    </form>



        </div>
        </div>

        <script>
            
            let input_file = document.querySelector('input[type="file"]');
            let picDisplay = document.getElementById('pic_label'); 
            input_file.onchange = (e) => {

            let file = e.target.files[0];


            let url = URL.createObjectURL(file);

            picDisplay.style.background = `url(${url}) center / cover no-repeat`;

            // Free up memory space (better perfomance)
            setTimeout(() => {
                URL.revokeObjectURL(url);
            }, 100)
        }

        // Declaring variables for banner
            let banner_input = document.getElementById('banner_image');
            let banLabel = document.getElementById('banner_label');


            // Live preview for banner

            banner_input.onchange = (e) => {

            let file = e.target.files[0];


            let url = URL.createObjectURL(file);

            banLabel.style.background = `url(${url}) center / cover no-repeat`;


            // Free up memory space (better perfomance)
            setTimeout(() => {
                URL.revokeObjectURL(url);
            }, 100)
        }
        </script>
</body>
</html>