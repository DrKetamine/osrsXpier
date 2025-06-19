<head>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
    <meta charset="UTF-8">
</head>
<body style="overflow: hidden;">
<x-header />
    <div class="staticWrap">
        <div class="staticFormCont">
            <form method="POST" action="/register">
                @csrf
                <h2>Registration</h2>
                <div class="inputsWrap">
                    <div class="pathName marg inputs">
                        <label for="name">Name</label>
                        <input class="inpt1" name="name" id="name" required>
                    </div>

                    <div class="pathName marg inputs">
                        <label for="email">Email</label>
                        <input class="inpt1" name="email" type="email" id="email" required>
                    </div>

                    <div class="dual">
                        <div class="pathName marg inputs">
                            <label for="password">Password</label>
                            <input class="inpt1" name="password" type="password" id="password" required>
                        </div>

                        <div class="pathName marg inputs">
                            <label for="password_confirmation">Confirm Password</label>
                            <input class="inpt1" name="password_confirmation" type="password" id="password_confirmation" required>
                        </div>
                    </div>
                </div>
                <button class="btn1" type="submit">Register</button>
            </form>
        </div>
    </div>
</body>