<head>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
    <meta charset="UTF-8">
</head>
<body style="overflow: hidden;">
<x-header />
    <div class="staticWrap">
        <div class="staticFormCont">
            <form method="POST" action="/login">
                @csrf
                <h2>Login</h2>
                <div class="inputsWrap">
                    <div class="pathName marg inputs">
                        <label for="email">Email</label>
                        <input name="email" type="email">
                    </div>
                    <div class="pathName marg inputs">
                        <label for="password">Password</label>
                        <input name="password" type="password">
                
                    </div>
                </div>
                <button class="btn1" type="submit">Login</button>
            </form>
        </div>
    </div>
</body>