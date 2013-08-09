<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : AccountConfirmation.xsl
    Created on : June 23, 2013, 8:33 PM
    Author     : root
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/AccountConfirmation">
        <div id="MainBody">
            
            <xsl:for-each select="LinkExpired">
                <div style="padding: 20px;">
                    This link has expired.
                </div>
            </xsl:for-each>
       
            <xsl:for-each select="AccountConfirmed">
                <div style="padding: 20px;">
                    Welcome to The Socialer! Your account has been confirmed.
                </div>
            </xsl:for-each>
       
            <xsl:for-each select="AlreadyConfirmed">
                <div style="padding: 20px;">
                    Your account has already been confirmed.
                </div>
            </xsl:for-each>

        <xsl:for-each select="AccountConfirmationSent">
            <div style="padding: 20px;">
                <h2>Please check your email</h2> 
                Because you typed in your email address, it's important we make sure we've got it right so you can login in the future. 
                This will only happen once and should only take a moment. If an email from the Socialer hasn't arrived in the next few seconds check your junk folder.
            </div>
        </xsl:for-each>
        
        </div>
                
                                                
    </xsl:template>


 
</xsl:stylesheet>
