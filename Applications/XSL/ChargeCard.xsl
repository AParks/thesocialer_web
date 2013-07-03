<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : ChargeCard.xsl
    Created on : June 5, 2013, 3:05 PM
    Author     : root
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/ChargeCard">
        <div id="MainBody">
            <div style="padding: 20px;">
                 <xsl:for-each select="Paid">
                    Your payment has been received. Please check your email for a receipt.           
                </xsl:for-each>
                 <xsl:for-each select="NotPaid">
                     <h4>Payment System Error!</h4>
                     Your payment could NOT be processed (i.e., you have not been charged) 
                     because the payment system rejected the transaction. You can try again or use another card.
                </xsl:for-each>                     
            </div>
        </div>
        
                        
    </xsl:template>

</xsl:stylesheet>
