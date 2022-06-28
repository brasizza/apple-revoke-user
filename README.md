
## Apple revoke API

This is a sample code to build a endpoint to revoke your user in your application
Some orientations about it:

1 - When your user log in your app or a place that uses the Apple's login, you will need to store their token and the code if you want to make the revoke.

2 - Unfortunately this code can be used just **ONCE** and just for 5 minutes, so you will need to force your user to log in again in your application to get the new token and the new code


## How to get the informations to build it

1 - You have to get your TeamId (its in the top screen in the right )
[https://developer.apple.com/account/resources/certificates/list]()

2 - You have to get your BundleId 
[https://developer.apple.com/account/resources/identifiers/list/bundleId]()

3 - You have to create a key that allows you to do a Sign In
[https://developer.apple.com/account/resources/authkeys/list]()
*This step you have to pay attention, because you can download your key just once, so keep it safe!*


Then you can put this values in your .env VALUE to be easier to get it

APPLE_KID is the name of your key that you generate in step 3
APPLE_TEAM_ID is your TeamId that you get in step 1
**You don't need to change the APPLE_AUD_URL value**
APPLE_BUNDLE is your apple package name that you get in step 2
APPLE_KEY_VALUE is your certificate content that you downloaded in the step 3, and open as a text and put it here

```
APPLE_KID="IKFIFHFISC"
APPLE_TEAM_ID="PU88889888"
APPLE_AUD_URL="https://appleid.apple.com"
APPLE_BUNDLE="br.com.your.bundle"
APPLE_KEY_VALUE="-----BEGIN PRIVATE KEY-----
THE CONTENT OF YOUR PRIVATE KEY HERE!
-----END PRIVATE KEY-----"
```

With all this in your code, you are able to consume the api called 'revoke' 
