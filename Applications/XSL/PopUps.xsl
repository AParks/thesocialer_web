<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : PopUps.xsl
    Created on : May 29, 2013, 1:34 PM
    Author     : aparks
    Description:
        Purpose of transformation follows.
-->
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/Popups">
        <div id='img'>
            <div id='popup-desc-start'>Socialer Popups are ... </div>
            <div id='popup-desc'>unique events hosted by and for members of our community.</div>
        </div>
        <div id="MainBody">
            
            <ol id="LocationList">
                <xsl:for-each select="images/image">
                    <li class="loc">
                        <div class="TitleLocationContainer" style="opacity: 0.9;">
                            <div class="DateContainer">
                                <div class="DateContainer_Date">
                                    <xsl:value-of select="./DateObject/@shortMonth" />
                                    <br/>
                                    <xsl:value-of select="./DateObject/@date" />
                                </div>
                            </div>
                            <div class="EventTitle">
                                <xsl:value-of select="./@headline"/>
                                <div class="EventPrice" style="font-family: comfortaabold">
                                    $<xsl:value-of select="./@price"/>
                                </div>
                            </div>
                            

                            <!--  <div class="EventLocation">
                                <xsl:value-of select="./@desc"/>
                            </div>-->
                        </div>

                        <a href="/location/featured/{./@id}" style="background-image: url({.});">
                            
                            <!-- <img class="PopUpLocationPicture" src="{.}"/> -->
                        </a>
                        

                    </li>
                
                </xsl:for-each>
            </ol>
        </div>

    </xsl:template>

  


</xsl:stylesheet>
