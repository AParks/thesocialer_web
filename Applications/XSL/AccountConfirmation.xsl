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

            <xsl:for-each select="AccountConfirmed">
                <div style="padding: 20px;">
                    Welcome to The Socialer! Your account has been confirmed.
                </div>
            
            </xsl:for-each>
       
        
            <xsl:for-each select="AccountConfirmationSent">
                <div style="padding: 20px;">
                    Welcome to The Socialer! Please check your email to confirm your account registration.
                </div>
            </xsl:for-each>
        
        </div>
                
                                                
    </xsl:template>


 
</xsl:stylesheet>
