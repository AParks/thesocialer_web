<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/ResetPassword">
        <div id="MainBody">
            <h1>Reset Your Password</h1>
            <div id="ResetPassContainer">
                <form id="ResetPasswordForm" autocomplete="off" method="post" action="" >
                    <div>
                        <input id="password" placeholder="New Password" type='password' name="newpass"/>
                    </div>
                    <div class="reset">
                        <input type="checkbox" class="passcheck"/>
                        <label for="passcheck">Show password </label>
                    </div>
              <br/>
                    <button id="ResetPass" class="reset joinbutton">Reset</button>
                                
                </form>
            </div>
            <div class="clear"></div>
        </div>
    </xsl:template>

    <xsl:template match="/ResetPasswordInvalid">
        <div id="MainBody">
            <h1>Reset Your Password</h1>
            <div id="ResetPassContainer">
                <h2>Oops!</h2>
                It looks like your link is expired, has already been used, or you got here by accident. If you forgot your password and need to reset it, please follow the steps on the <a href="http://www.thesocialer.com">login page</a>
            </div>
            <div class="clear"></div>
        </div>
    </xsl:template>

</xsl:stylesheet>
