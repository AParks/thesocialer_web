<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/AdminLocationManager">
    <div id="MainBody">
      <xsl:apply-templates select="Locations" />
      <xsl:call-template name="AddLocation" />
    </div>
  </xsl:template>

  <xsl:template match="Locations">
    <table id="Locations">
      <tr>
        <th>ID</th><th>Name</th><th>Street Address</th><th>Description</th><th>Website</th><th>Yelp Id</th><th>Actions</th>
      </tr>
      <xsl:for-each select="Location">
        <tr>
          <td><xsl:value-of select="@id" /></td>
          <td><xsl:value-of select="@name" /></td>
          <td><xsl:value-of select="@streetAddress" /></td>
          <td><xsl:value-of select="@description" /></td>
          <td><xsl:value-of select="@website" /></td>
          <td><xsl:value-of select="@yelpId" /></td>
          <td class="actions"><a href="#" class="delete">Delete</a></td>
         </tr>
      </xsl:for-each>
    </table>
  </xsl:template>

  <xsl:template name="AddLocation">
    <table id="AddLocationForm">
      <tr>
        <th>Name</th>
        <td><input name="name" /></td>
      </tr>
      <tr>
        <th>Street Address</th>
        <td><input name="streetAddress" /></td>
      </tr>
      <tr>
        <th>Description</th>
        <td><textarea name="description"></textarea></td>
      </tr>
      <tr>
        <th>website</th>
        <td><input name="website" /></td>
      </tr>
      <!--
      <tr>
        <th>Yelp ID</th>
        <td><input name="yelpId" /></td>
      </tr>
      -->
      <tr><td colspan="2" class="actions"><button name="Submit">Add</button></td></tr>
    </table>
  </xsl:template>


</xsl:stylesheet>
