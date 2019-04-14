<html>
    <head>
        <title>
            
        </title>
        {{ HTML::script('assets/js/jquery-1.11.1.js') }}
        {{ HTML::script('assets/js/clinic-ajax.js') }}
    </head>
    <body>
        <div id="loginfrm">
        {{Form::open(array("","id"=>"frm"))}}
        <!-- <form id="frm"></form> -->
        <label id="username-label">Username</label>
        <input type="text" name="username" id="username">
        <label id="password-label">Password</label>
        {{Form::password('password',array("class"=>"frmControl","id"=>"password"))}}
        {{Form::submit('Create User')}}
        </form>
        
        <div id="hihi"></div>
    </div>
        
        <div id="change-doc">
            <select id="docch">
                <option value="0">Select a Doctor</option>
                <option value="1">Rizvi</option>
                <option value="2">Mohamed</option>
                <option value="3">Ansar</option>
                <option value="4">Raheem</option>
            </select>
            
        </div>
        
    </body>
</html>