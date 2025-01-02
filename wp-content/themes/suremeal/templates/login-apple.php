<?php /* Template Name: login-apple */ ?>
<html>
<head>
</head>
<body>
<script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>
<div id="appleid-signin" data-color="black" data-border="true" data-type="sign in"></div>
<script type="text/javascript">
    AppleID.auth.init({
        clientId : '103.56.158.161', // Thay bằng Client ID chính xác
        scope : 'quyhop23@gmai.com',
        redirectURI : 'https://suremeal.qixtech.com/login-apple', // URL callback chính xác
        state : 'state123',
        nonce : 'nonce123',
        usePopup : true
    });
</script>


</body>
</html
