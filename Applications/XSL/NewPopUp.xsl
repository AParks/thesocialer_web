<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : NewPopUp.xsl
    Created on : July 29, 2013, 12:43 PM
    Author     : anna parks
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>

    <xsl:template match="/NewPopUp">
        <div id="MainBody">
            <div class="header">
                <div class='step-container active'>
                    <div class='step'>
                        <i class="icon-edit icon-large"></i> 
                    </div>
                    <br/>Create a popup
                </div>
                <div class='step-container'>
                    <div class='step'>
                        <i class="icon-calendar icon-large"></i> 
                    </div>
                    <br/>When and where?
                </div>
                <div class='step-container'>
                    <div class='step'>
                        <i class="icon-camera icon-large"></i> 
                    </div>
                    <br/>Extras
                </div>

            </div>
            <form id='new_pop' action="/A/FeaturedJSON.php" method="post" enctype="multipart/form-data" onSubmit="return validateForm()">

                <div id='popup'>


                    <fieldset id='first' class='active'>
                        <input id="headinput" type="text" name="headline" placeholder='Event Headline' class='required'/>
                        <input id="subheadinput" type="text" name="sub_headline" placeholder='Event Subheadline (Optional)'/>
                        <textarea name="description"  class='required' placeholder="Describe what you will share and why you think it's awesome"></textarea>
                        <br/>
                        <label>
                            <input checked="checked" type="radio" name="is_private" value="false"/>
                            Public
                        </label>
                        <label>
                            <input type="radio" name="is_private" value="true" />
                            Invite Only     
                        </label>

                    </fieldset>
                    <fieldset id='second' class='inactive'>
                        <span>From: </span><input class="date datepicker required" type="text" name="startDate"/>
                        <input class="date time required" type="text" name="startTime" placeholder="HH:MM" />
                        <a id='end_time'>End time?</a>
                        <br/>
                        <span id='to'>To: </span><input class="date datepicker" type="text" name="endDate" />
                        <input class="date time" type="text" name="endTime" placeholder="HH:MM"/>
                        <br/>          
                        <input class='required' type="text" id='popup_location' name="location" placeholder="Location" />
                        <div id="map-canvas"></div>
                    </fieldset>
                    <fieldset id='last' class='inactive'>
                        <!--     <input type="filepicker-dragdrop" data-fp-button-text='Add photos' data-fp-button-class='add-photo' data-fp-extensions='.jpg,.png,.jpeg,.gif,.bmp' data-fp-apikey='ATkirnHVuRJyx5NEogt6gz' data-fp-multiple="true"/>
                        -->
                        <div style="height:0px;margin-left: 80px;">
                            <input class='required' type="file" name="file[]"  id="file" multiple='true'/>
                        </div>
                        <div class='add-photo'>Add photos</div>
                        <br/>
                        Price: $ <input id="extrainput" type="text" min="0" name="price" placeholder='Price' value='0'/>
                        <error></error><br/>
                        Max number of guests: <input type="number" id="spots" name="spots" value='10' />
                    </fieldset>
                    <input type="hidden" name="startDate" value="" />
                    <input type="hidden" name="endDate" value="" />
                    <input type="hidden" name="markup" value="" />
                    <input type="hidden" name="action" value="create" />
                    <input type="hidden" name="host" value="{./Viewer/@userId}" />

            
                </div>
                <div class="modal-footer">
                    <a class='butn'>Back</a>
                    <a class="new_popup_button" id='next'>Next</a>
                    <input type="submit" class='new_popup_button' name="submit" value="Submit" />
                </div>
            </form>

        </div>
    </xsl:template>

</xsl:stylesheet>
