<?php

namespace IslamCompanion;

/**
 * This class implements the main plugin class
 * It contains functions that implement the filter, actions and hooks defined in the application configuration
 * 
 * It is used to implement the main functions of the plugin
 * 
 * @category   IslamCompanion
 * @package    IslamCompanion
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    2.0.0
 * @link       N.A
 */
final class IslamCompanionSettings extends \Framework\Application\WordPress\Settings
{
    /**
     * Holds the plugin settings values. Including default values and callback function names
     */
    private $plugin_settings;

	/** 
     * Fetches data used to populate the settings form fields 
     * 
	 * It fetches the data used to popular the settings form select boxes
	 * 
     * @since    2.0.0
	 * 
	 * @return array $field_data the data used to populate to the settings fields
	 * it is an array with following keys:
	 * narrator_language_mapping => contains all the language and narrator data
	 *                              it is an array of values
	 *                              each value is an array with following keys: language and narrator
	 * division => the Holy Quran divisions
	 * language => the list of all supported languages. it is an array of language strings
	 * narrator => the list of all supported narrators. it is an array of narrator strings
	 * data_source => the data sources that are supported by the plugin. it can have 2 possible values:
	 * local => the Holy Quran data is stored locally. it requires importing the data
	 * remote => the Holy Quran data is fetched from a remote api host. this option is slower and less reliable
     */
    private function GetSettingsFieldData()
    {
    	/** The field data */
    	$field_data                                                      = array();
		/** The language translator data */		
		$field_data['encoded_narrator_language_mapping']                 = 'W3sibGFuZ3VhZ2UiOiJBemVyYmFpamFuaSIsIm5hcnJhdG9yIjoiVmFzaW0gTWFtbWFkYWxpeWV2IGFuZCBaaXlhIEJ1bnlhZG92In0seyJsYW5ndWFnZSI6IkFtaGFyaWMgIiwibmFycmF0b3IiOiJNdWhhbW1lZCBTYWRpcSBhbmQgTXVoYW1tZWQgU2FuaSBIYWJpYiJ9LHsibGFuZ3VhZ2UiOiJBcmFiaWMiLCJuYXJyYXRvciI6IktpbmcgRmFoYWQgUXVyYW4gQ29tcGxleCJ9LHsibGFuZ3VhZ2UiOiJBcmFiaWMiLCJuYXJyYXRvciI6IkphbGFsIGFkLURpbiBhbC1NYWhhbGxpIGFuZCBKYWxhbCBhZC1EaW4gYXMtU3V5dXRpIn0seyJsYW5ndWFnZSI6IkF6ZXJiYWlqYW5pIiwibmFycmF0b3IiOiJBbGlraGFuIE11c2F5ZXYifSx7Imxhbmd1YWdlIjoiQm9zbmlhbiIsIm5hcnJhdG9yIjoiTXVzdGFmYSBNbGl2byJ9LHsibGFuZ3VhZ2UiOiJCb3NuaWFuIiwibmFycmF0b3IiOiJCZXNpbSBLb3JrdXQifSx7Imxhbmd1YWdlIjoiQmVuZ2FsaSIsIm5hcnJhdG9yIjoiTXVoaXVkZGluIEtoYW4ifSx7Imxhbmd1YWdlIjoiQmVuZ2FsaSIsIm5hcnJhdG9yIjoiWm9odXJ1bCBIb3F1ZSJ9LHsibGFuZ3VhZ2UiOiJCdWxnYXJpYW4iLCJuYXJyYXRvciI6IlR6dmV0YW4gVGhlb3BoYW5vdiJ9LHsibGFuZ3VhZ2UiOiJBbWF6aWdoIiwibmFycmF0b3IiOiJSYW1kYW5lIEF0IE1hbnNvdXIifSx7Imxhbmd1YWdlIjoiQ3plY2giLCJuYXJyYXRvciI6IkEuIFIuIE55a2wifSx7Imxhbmd1YWdlIjoiQ3plY2giLCJuYXJyYXRvciI6IlByZWtsYWQgSS4gSHJiZWsifSx7Imxhbmd1YWdlIjoiR2VybWFuIiwibmFycmF0b3IiOiJBbWlyIFphaWRhbiJ9LHsibGFuZ3VhZ2UiOiJHZXJtYW4iLCJuYXJyYXRvciI6IkFkZWwgVGhlb2RvciBLaG91cnkifSx7Imxhbmd1YWdlIjoiR2VybWFuIiwibmFycmF0b3IiOiJBLiBTLiBGLiBCdWJlbmhlaW0gYW5kIE4uIEVseWFzIn0seyJsYW5ndWFnZSI6Ikdlcm1hbiIsIm5hcnJhdG9yIjoiQWJ1IFJpZGEgTXVoYW1tYWQgaWJuIEFobWFkIGlibiBSYXNzb3VsIn0seyJsYW5ndWFnZSI6IkRpdmVoaSIsIm5hcnJhdG9yIjoiT2ZmaWNlIG9mIHRoZSBQcmVzaWRlbnQgb2YgTWFsZGl2ZXMifSx7Imxhbmd1YWdlIjoiU3BhbmlzaCIsIm5hcnJhdG9yIjoiTXVoYW1tYWQgSXNhIEdhcmNcdTAwZWRhIn0seyJsYW5ndWFnZSI6IkVuZ2xpc2giLCJuYXJyYXRvciI6Ik1vaGFtbWVkIE1hcm1hZHVrZSBXaWxsaWFtIFBpY2t0aGFsbCJ9LHsibGFuZ3VhZ2UiOiJFbmdsaXNoIiwibmFycmF0b3IiOiJBbGkgUXVsaSBRYXJhaSJ9LHsibGFuZ3VhZ2UiOiJFbmdsaXNoIiwibmFycmF0b3IiOiJIYXNhbiBhbC1GYXRpaCBRYXJpYnVsbGFoIGFuZCBBaG1hZCBEYXJ3aXNoIn0seyJsYW5ndWFnZSI6IkVuZ2xpc2giLCJuYXJyYXRvciI6IlNhaGVlaCBJbnRlcm5hdGlvbmFsIn0seyJsYW5ndWFnZSI6IkVuZ2xpc2giLCJuYXJyYXRvciI6Ik11aGFtbWFkIFNhcndhciJ9LHsibGFuZ3VhZ2UiOiJFbmdsaXNoIiwibmFycmF0b3IiOiJNb2hhbW1hZCBIYWJpYiBTaGFraXIifSx7Imxhbmd1YWdlIjoiRW5nbGlzaCIsIm5hcnJhdG9yIjoiRW5nbGlzaCBUcmFuc2xpdGVyYXRpb24ifSx7Imxhbmd1YWdlIjoiRW5nbGlzaCIsIm5hcnJhdG9yIjoiQWJkdWxsYWggWXVzdWYgQWxpIn0seyJsYW5ndWFnZSI6IkVuZ2xpc2giLCJuYXJyYXRvciI6IkEuIEouIEFyYmVycnkifSx7Imxhbmd1YWdlIjoiU3BhbmlzaCIsIm5hcnJhdG9yIjoiSnVsaW8gQ29ydGVzIn0seyJsYW5ndWFnZSI6IlNwYW5pc2giLCJuYXJyYXRvciI6IlJhXHUwMGZhbCBHb256XHUwMGUxbGV6IEJcdTAwZjNybmV6In0seyJsYW5ndWFnZSI6IkVuZ2xpc2giLCJuYXJyYXRvciI6IkFobWVkIEFsaSJ9LHsibGFuZ3VhZ2UiOiJFbmdsaXNoIiwibmFycmF0b3IiOiJBaG1lZCBSYXphIEtoYW4ifSx7Imxhbmd1YWdlIjoiRW5nbGlzaCIsIm5hcnJhdG9yIjoiQWJkdWwgTWFqaWQgRGFyeWFiYWRpIn0seyJsYW5ndWFnZSI6IkVuZ2xpc2giLCJuYXJyYXRvciI6Ik11aGFtbWFkIFRhcWktdWQtRGluIGFsLUhpbGFsaSBhbmQgTXVoYW1tYWQgTXVoc2luIEtoYW4ifSx7Imxhbmd1YWdlIjoiRW5nbGlzaCIsIm5hcnJhdG9yIjoiVGFsYWwgSXRhbmkifSx7Imxhbmd1YWdlIjoiRW5nbGlzaCIsIm5hcnJhdG9yIjoiQWJ1bCBBbGEgTWF1ZHVkaSJ9LHsibGFuZ3VhZ2UiOiJFbmdsaXNoIiwibmFycmF0b3IiOiJXYWhpZHVkZGluIEtoYW4ifSx7Imxhbmd1YWdlIjoiUGVyc2lhbiIsIm5hcnJhdG9yIjoiSHVzc2FpbiBBbnNhcmlhbiJ9LHsibGFuZ3VhZ2UiOiJQZXJzaWFuIiwibmFycmF0b3IiOiJOYXNlciBNYWthcmVtIFNoaXJhemkifSx7Imxhbmd1YWdlIjoiRnJlbmNoIiwibmFycmF0b3IiOiJNdWhhbW1hZCBIYW1pZHVsbGFoIn0seyJsYW5ndWFnZSI6IlBlcnNpYW4iLCJuYXJyYXRvciI6IkFiZG9sTW9oYW1tYWQgQXlhdGkifSx7Imxhbmd1YWdlIjoiUGVyc2lhbiIsIm5hcnJhdG9yIjoiQWJvbGZhemwgQmFocmFtcG91ciJ9LHsibGFuZ3VhZ2UiOiJQZXJzaWFuIiwibmFycmF0b3IiOiJNb3N0YWZhIEtob3JyYW1kZWwifSx7Imxhbmd1YWdlIjoiUGVyc2lhbiIsIm5hcnJhdG9yIjoiQmFoYSdvZGRpbiBLaG9ycmFtc2hhaGkifSx7Imxhbmd1YWdlIjoiUGVyc2lhbiIsIm5hcnJhdG9yIjoiTW9oYW1tYWQgU2FkZXFpIFRlaHJhbmkifSx7Imxhbmd1YWdlIjoiUGVyc2lhbiIsIm5hcnJhdG9yIjoiTW9oYW1tYWQgTWFoZGkgRm9vbGFkdmFuZCJ9LHsibGFuZ3VhZ2UiOiJQZXJzaWFuIiwibmFycmF0b3IiOiJTYXl5ZWQgSmFsYWxvZGRpbiBNb2p0YWJhdmkifSx7Imxhbmd1YWdlIjoiUGVyc2lhbiIsIm5hcnJhdG9yIjoiTW9oYW1tYWQgS2F6ZW0gTW9lenppIn0seyJsYW5ndWFnZSI6IlBlcnNpYW4iLCJuYXJyYXRvciI6Ik1haGRpIEVsYWhpIEdob21zaGVpIn0seyJsYW5ndWFnZSI6IkhpbmRpIiwibmFycmF0b3IiOiJNdWhhbW1hZCBGYXJvb3EgS2hhbiBhbmQgTXVoYW1tYWQgQWhtZWQifSx7Imxhbmd1YWdlIjoiSGF1c2EiLCJuYXJyYXRvciI6IkFidWJha2FyIE1haG1vdWQgR3VtaSJ9LHsibGFuZ3VhZ2UiOiJIaW5kaSIsIm5hcnJhdG9yIjoiU3VoZWwgRmFyb29xIEtoYW4gYW5kIFNhaWZ1ciBSYWhtYW4gTmFkd2kifSx7Imxhbmd1YWdlIjoiSW5kb25lc2lhbiIsIm5hcnJhdG9yIjoiSmFsYWwgYWQtRGluIGFsLU1haGFsbGkgYW5kIEphbGFsIGFkLURpbiBhcy1TdXl1dGkifSx7Imxhbmd1YWdlIjoiSW5kb25lc2lhbiIsIm5hcnJhdG9yIjoiTXVoYW1tYWQgUXVyYWlzaCBTaGloYWIgZXQgYWwuIn0seyJsYW5ndWFnZSI6IkluZG9uZXNpYW4iLCJuYXJyYXRvciI6IkluZG9uZXNpYW4gTWluaXN0cnkgb2YgUmVsaWdpb3VzIEFmZmFpcnMifSx7Imxhbmd1YWdlIjoiSXRhbGlhbiIsIm5hcnJhdG9yIjoiSGFtemEgUm9iZXJ0byBQaWNjYXJkbyJ9LHsibGFuZ3VhZ2UiOiJKYXBhbmVzZSIsIm5hcnJhdG9yIjoiVW5rbm93biJ9LHsibGFuZ3VhZ2UiOiJLb3JlYW4iLCJuYXJyYXRvciI6IlVua25vd24ifSx7Imxhbmd1YWdlIjoiS3VyZGlzaCIsIm5hcnJhdG9yIjoiQnVyaGFuIE11aGFtbWFkLUFtaW4ifSx7Imxhbmd1YWdlIjoiTWFsYXlhbGFtIiwibmFycmF0b3IiOiJDaGVyaXlhbXVuZGFtIEFiZHVsIEhhbWVlZCBhbmQgS3VuaGkgTW9oYW1tZWQgUGFyYXBwb29yIn0seyJsYW5ndWFnZSI6Ik1hbGF5IiwibmFycmF0b3IiOiJBYmR1bGxhaCBNdWhhbW1hZCBCYXNtZWloIn0seyJsYW5ndWFnZSI6Ik1hbGF5YWxhbSIsIm5hcnJhdG9yIjoiTXVoYW1tYWQgS2FyYWt1bm51IGFuZCBWYW5pZGFzIEVsYXlhdm9vciJ9LHsibGFuZ3VhZ2UiOiJEdXRjaCIsIm5hcnJhdG9yIjoiRnJlZCBMZWVtaHVpcyJ9LHsibGFuZ3VhZ2UiOiJEdXRjaCIsIm5hcnJhdG9yIjoiU29maWFuIFMuIFNpcmVnYXIifSx7Imxhbmd1YWdlIjoiTm9yd2VnaWFuIiwibmFycmF0b3IiOiJFaW5hciBCZXJnIn0seyJsYW5ndWFnZSI6IkR1dGNoIiwibmFycmF0b3IiOiJTYWxvbW8gS2V5emVyIn0seyJsYW5ndWFnZSI6IlBvcnR1Z3Vlc2UiLCJuYXJyYXRvciI6IlNhbWlyIEVsLUhheWVrIn0seyJsYW5ndWFnZSI6IlBvbGlzaCIsIm5hcnJhdG9yIjoiSlx1MDBmM3plZmEgQmllbGF3c2tpZWdvIn0seyJsYW5ndWFnZSI6IlJ1c3NpYW4iLCJuYXJyYXRvciI6IkVsbWlyIEt1bGlldiJ9LHsibGFuZ3VhZ2UiOiJSdXNzaWFuIiwibmFycmF0b3IiOiJHb3JkeSBTZW15b25vdmljaCBTYWJsdWtvdiJ9LHsibGFuZ3VhZ2UiOiJSdXNzaWFuIiwibmFycmF0b3IiOiJWLiBQb3Jva2hvdmEifSx7Imxhbmd1YWdlIjoiUnVzc2lhbiIsIm5hcnJhdG9yIjoiTWFnb21lZC1OdXJpIE9zbWFub3ZpY2ggT3NtYW5vdiJ9LHsibGFuZ3VhZ2UiOiJSb21hbmlhbiIsIm5hcnJhdG9yIjoiR2VvcmdlIEdyaWdvcmUifSx7Imxhbmd1YWdlIjoiUnVzc2lhbiIsIm5hcnJhdG9yIjoiQWJ1IEFkZWwifSx7Imxhbmd1YWdlIjoiUnVzc2lhbiIsIm5hcnJhdG9yIjoiRWxtaXIgS3VsaWV2ICh3aXRoIEFiZCBhci1SYWhtYW4gYXMtU2FhZGkncyBjb21tZW50YXJpZXMpIn0seyJsYW5ndWFnZSI6IlJ1c3NpYW4iLCJuYXJyYXRvciI6Ik1pbmlzdHJ5IG9mIEF3cWFmLCBFZ3lwdCJ9LHsibGFuZ3VhZ2UiOiJSdXNzaWFuIiwibmFycmF0b3IiOiJJZ25hdHkgWXVsaWFub3ZpY2ggS3JhY2hrb3Zza3kifSx7Imxhbmd1YWdlIjoiU3dlZGlzaCIsIm5hcnJhdG9yIjoiS251dCBCZXJuc3RyXHUwMGY2bSJ9LHsibGFuZ3VhZ2UiOiJTb21hbGkiLCJuYXJyYXRvciI6Ik1haG11ZCBNdWhhbW1hZCBBYmR1aCJ9LHsibGFuZ3VhZ2UiOiJTaW5kaGkiLCJuYXJyYXRvciI6IlRhaiBNZWhtb29kIEFtcm90aSJ9LHsibGFuZ3VhZ2UiOiJBbGJhbmlhbiIsIm5hcnJhdG9yIjoiU2hlcmlmIEFobWV0aSJ9LHsibGFuZ3VhZ2UiOiJBbGJhbmlhbiIsIm5hcnJhdG9yIjoiRmV0aSBNZWhkaXUifSx7Imxhbmd1YWdlIjoiQWxiYW5pYW4iLCJuYXJyYXRvciI6Ikhhc2FuIEVmZW5kaSBOYWhpIn0seyJsYW5ndWFnZSI6IlN3YWhpbGkiLCJuYXJyYXRvciI6IkFsaSBNdWhzaW4gQWwtQmFyd2FuaSJ9LHsibGFuZ3VhZ2UiOiJUdXJraXNoIiwibmFycmF0b3IiOiJTdWF0IFlpbGRpcmltIn0seyJsYW5ndWFnZSI6IlR1cmtpc2giLCJuYXJyYXRvciI6IkFiZHVsYmFraSBHb2xwaW5hcmxpIn0seyJsYW5ndWFnZSI6IlR1cmtpc2giLCJuYXJyYXRvciI6IkRpeWFuZXQgSXNsZXJpIn0seyJsYW5ndWFnZSI6IlR1cmtpc2giLCJuYXJyYXRvciI6Ik11aGFtbWV0IEFiYXkifSx7Imxhbmd1YWdlIjoiVHVya2lzaCIsIm5hcnJhdG9yIjoiQWxcdTAxMzAgQnVsYVx1MDBlNyJ9LHsibGFuZ3VhZ2UiOiJUYWppayIsIm5hcnJhdG9yIjoiQWJkb2xNb2hhbW1hZCBBeWF0aSJ9LHsibGFuZ3VhZ2UiOiJUdXJraXNoIiwibmFycmF0b3IiOiJZYXNhciBOdXJpIE96dHVyayJ9LHsibGFuZ3VhZ2UiOiJUYW1pbCIsIm5hcnJhdG9yIjoiSmFuIFR1cnN0IEZvdW5kYXRpb24ifSx7Imxhbmd1YWdlIjoiVHVya2lzaCIsIm5hcnJhdG9yIjoiRWxtYWxpbGkgSGFtZGkgWWF6aXIifSx7Imxhbmd1YWdlIjoiVGF0YXIiLCJuYXJyYXRvciI6Illha3ViIElibiBOdWdtYW4ifSx7Imxhbmd1YWdlIjoiVHVya2lzaCIsIm5hcnJhdG9yIjoiRWRpcCBZXHUwMGZja3NlbCJ9LHsibGFuZ3VhZ2UiOiJUaGFpIiwibmFycmF0b3IiOiJLaW5nIEZhaGFkIFF1cmFuIENvbXBsZXgifSx7Imxhbmd1YWdlIjoiVHVya2lzaCIsIm5hcnJhdG9yIjoiRGl5YW5ldCBWYWtmaSJ9LHsibGFuZ3VhZ2UiOiJUdXJraXNoIiwibmFycmF0b3IiOiJTdWxleW1hbiBBdGVzIn0seyJsYW5ndWFnZSI6IlV6YmVrIiwibmFycmF0b3IiOiJNdWhhbW1hZCBTb2RpayBNdWhhbW1hZCBZdXN1ZiJ9LHsibGFuZ3VhZ2UiOiJVcmR1IiwibmFycmF0b3IiOiJBaG1lZCBBbGkifSx7Imxhbmd1YWdlIjoiVXJkdSIsIm5hcnJhdG9yIjoiRmF0ZWggTXVoYW1tYWQgSmFsYW5kaHJ5In0seyJsYW5ndWFnZSI6IlVyZHUiLCJuYXJyYXRvciI6IlRhaGlyIHVsIFFhZHJpIn0seyJsYW5ndWFnZSI6IlVyZHUiLCJuYXJyYXRvciI6IlN5ZWQgWmVlc2hhbiBIYWlkZXIgSmF3YWRpIn0seyJsYW5ndWFnZSI6IlVyZHUiLCJuYXJyYXRvciI6Ik11aGFtbWFkIEp1bmFnYXJoaSJ9LHsibGFuZ3VhZ2UiOiJVcmR1IiwibmFycmF0b3IiOiJBeWF0b2xsYWggTXVoYW1tYWQgSHVzc2FpbiBOYWphZmkifSx7Imxhbmd1YWdlIjoiVXlnaHVyIiwibmFycmF0b3IiOiJNdWhhbW1hZCBTYWxlaCJ9LHsibGFuZ3VhZ2UiOiJVcmR1IiwibmFycmF0b3IiOiJBaG1lZCBSYXphIEtoYW4ifSx7Imxhbmd1YWdlIjoiVXJkdSIsIm5hcnJhdG9yIjoiQWJ1bCBBJ2FsYSBNYXVkdWRpIn0seyJsYW5ndWFnZSI6IkNoaW5lc2UiLCJuYXJyYXRvciI6Ik1hIEppYW4ifSx7Imxhbmd1YWdlIjoiQ2hpbmVzZSIsIm5hcnJhdG9yIjoiTWEgSmlhbiJ9XQ==';
		$field_data['narrator_language_mapping']                         = $this->GetComponent("encryption")->DecodeData($field_data['encoded_narrator_language_mapping']);	
		
		/** The list of distinct languages */
		$languages                                                       = array();
		/** The list of distinct narrators */
		$narrators                                                       = array();
		/** The list of distinct languages and narrators is determined */
		for ($count = 0; $count < count ($field_data['narrator_language_mapping']); $count++) {
			/** Single narrator language mapping */ 
		    $narrator_language                                           = $field_data['narrator_language_mapping'][$count];
			/** The language */
			$language                                                    = $narrator_language["language"];
			/** The narrator */
			$narrator                                                    = $narrator_language["narrator"];
			/** If the language has not yet been added to the list of distinct languages, then it is added */
			if (!in_array($language, $languages))
			    $languages[]                                             = $language;	
			/** If the narrator has not yet been added to the list of distinct narrators, then it is added */
			if (!in_array($narrator, $narrators))
			    $narrators[]                                             = $narrator;	
		}
			
        /** The divisions data */
		$field_data['division']                                          = array("Hizb","Juz","Manzil","Pages","Ruku");		
		
		/** The language data */
		$field_data['language']                                          = $languages;
		
		/** The narrator data */
		$field_data['narrator']                                          = $narrators;
				
		/** The data sources */
		$field_data['data_source']                                       = array("Local","Remote");
		
		return $field_data;
    }
    
    /**
     * Options page callback
     *
     * @since    2.0.0
     * @access   private
     */
    public function DisplaySettingsPage()
    {
    	try {       
	            /** The settings fields are displayed */
	            $settings_fields_html = $this->GetSettingsFieldsHtml();
	            $plugin_template_path = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR . "settings.html";
	            $plugin_text_domain   = $this->GetConfig("wordpress", "plugin_text_domain");
	        
	            /** The tag replacement array is built */
	            $tag_replacement_arr = array(
	                array(
	                "heading" => __("Islam Companion", $plugin_text_domain),
	                "data_import_heading" => __("Data Import Status", $plugin_text_domain),
	                "ajax_nonce" => wp_create_nonce("islam-companion"),
	                "form_fields" => $settings_fields_html,	                
	                "powered_by_text" => __("Powered By", $plugin_text_domain),
	                "report_bug_text" => __("Report a bug", $plugin_text_domain),
	                "suggest_feature_text" => __("Suggest a feature", $plugin_text_domain)
	                )
	            );
	      
	            /** The settings page template is rendered */
	            $settings_page_html    = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path, $tag_replacement_arr);
	            /** The settings page html is displayed */
	            $this->GetComponent("application")->DisplayOutput($settings_page_html);
	    }
		catch(\Exception $e){
			$this->GetComponent("errorhandler")->ExceptionHandler($e);
		}
    }
    
    /**
     * Registers and adds settings using the WordPress api
     *
     * @since    2.0.0	 
     */
    public function InitializeAdminPage()
    {
    	try {
    	    /** The settings field data is fetched */
			$field_data                   = $this->GetSettingsFieldData();						
	        /** The plugin text domain */
	        $plugin_text_domain           = $this->GetConfig("wordpress", "plugin_text_domain");
	        /** The options id is fetched */
	        $options_id                   = $this->GetComponent("application")->GetOptionsId("options");			           
			/** The current plugin options are fetched */
			$options                      = $this->GetComponent("application")->GetPluginOptions($options_id);
			/** The narrator */
			$narrator                     = array("narrator"=>$options['narrator']);
			/** The narrator data is encoded */
			$narrator                     = $this->GetComponent("encryption")->EncodeData($narrator);
			/** The ajax nonce */
			$ajax_nonce                   = wp_create_nonce('islam-companion');							
			/** The value of the extra hidden field is set */
			$extra                        = ($field_data['encoded_narrator_language_mapping']."@".$narrator."@".$ajax_nonce);
	        /** The plugin settings are initialized */
			$this->plugin_settings = array(
			    /** The visible fields */
				"language" => array(
				    "name" => __('Language', $plugin_text_domain),
					"callback" => array("settings","DropdownFieldCallback"),					
					"hidden" => false,
					"short_name" => "language",
					"args" => array("options" => $field_data['language'],"default_value" => (isset($options['language']))?$options['language']:"English")
				),
				"narrator" => array(
				    "name" => __('Narrator', $plugin_text_domain),
					"callback" => array("settings","DropdownFieldCallback"),					
					"hidden" => false,
					"short_name" => "narrator",
					"args" => array("options" => $field_data['narrator'],"default_value" => isset($options['narrator'])?$options['narrator']:"Mohammed Marmaduke William Pickthall")
				),
				"division" => array(
				    "name" => __('Division', $plugin_text_domain),
					"callback" => array("settings","DropdownFieldCallback"),					
					"hidden" => false,
					"short_name" => "division",
					"args" => array("options" => $field_data['division'],"default_value" => isset($options['division'])?$options['division']:"ruku")
				),
				"data_source" => array(
				    "name" => __('Data Source', $plugin_text_domain),
					"callback" => array("settings","DropdownFieldCallback"),					
					"hidden" => false,
					"short_name" => "data_source",
					"args" => array("options" => $field_data['data_source'],"default_value" => isset($options['data_source'])?$options['data_source']:"remote")
				),
				/** The hidden fields */
				"division_number" => array(
				    "name" => __('Division Number', $plugin_text_domain),
					"callback" => array("settings","HiddenFieldCallback"),					
					"hidden" => true,
					"short_name" => "division_number",
					"args" => array("default_value" => "1")
				),
				"sura" => array(
				    "name" => __('Sura', $plugin_text_domain),
					"callback" => array("settings","HiddenFieldCallback"),					
					"hidden" => true,
					"short_name" => "sura",
					"args" => array("default_value" => "1")
				),
				"ayat" => array(
				    "name" => __('Ayat', $plugin_text_domain),
					"callback" => array("settings","HiddenFieldCallback"),					
					"hidden" => true,
					"short_name" => "ayat",
					"args" => array("default_value" => '1')
				),
				"ruku" => array(
				    "name" => __('Ruku', $plugin_text_domain),
					"callback" => array("settings","HiddenFieldCallback"),                    
					"hidden" => true,
					"short_name" => "ruku",
					"args" => array("default_value" => '1')
				),
				"extra" => array(
				    "name" => __('Extra', $plugin_text_domain),
					"callback" => array("settings","HiddenFieldCallback"),					
					"hidden" => true,
					"short_name" => "extra",
					"args" => array("default_value" => $extra)
				)
			);
		
	        $this->RegisterPluginOptions($this->plugin_settings);
		}
		catch(\Exception $e){
			$this->GetComponent("errorhandler")->ExceptionHandler($e);
		}
    }
        
    /**
     * Used to display the section information
     *
     * The section information is displayed
     * 
     * @since    2.0.0	 
     */
    public function PrintSectionInfo()
    {
    	/** The plugin text domain */
	    $plugin_text_domain           = $this->GetConfig("wordpress", "plugin_text_domain");
        echo __('Holy Quran Navigator Settings', $plugin_text_domain);
    }
    
    /** 
     * Displays the dropdown settings field
     * 
     * @param array $args an array containing the field type and option id
     */
    public function DropdownFieldCallback($args)
    {
        /** The options id is fetched */
	    $options_id           = $this->GetComponent("application")->GetOptionsId("options");	
        /** The path to the plugin template folder */
        $plugin_template_path = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR;		
        /** The plugin prefix */
        $plugin_prefix        = $this->GetConfig("wordpress", "plugin_prefix");
        /** The field name */
		$field_name           = $args['field_name'];
		/** The field value is set to the saved option value. If the saved option value does not exist then it is set to the default value **/
		$field_value          = $args['default_value'];
		/** The select option array is initialized */
		$select_options       = array();
		/** The information used to create the select dropdown */
        $dropdown_information = array(
            "options" => $args["options"],
            "name" => $field_name
        );
        
        /** The select options are built */
        for ($count = 0; $count < count($dropdown_information['options']); $count++) {        	
        	/** The select box text */
            $text          = $dropdown_information['options'][$count];
			/** The select box value text. it is converted to lower case if needed */
			$value         = ($lowercase_value)?strtolower($text):$text;
			/** The select box options */
            $select_options[] = array(
                "text" => $text,
                "value" => $value,
                "selected" => ($field_value == $value) ? "SELECTED" : ""
            );
        }
        
        /** The tag replacement array is built */
        $tag_replacement_arr = ($select_options);
        /** The select option template is rendered */
        $option_field_html   = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path . "option.html", $tag_replacement_arr);
        /** The tag replacement array is built */
        $tag_replacement_arr = array(
            array(
                "id" => $plugin_prefix . "_" . $field_name,
                "name" => $options_id . '[' . $field_name . ']',
                "options" => $option_field_html
            )
        );
        /** The select option template is rendered */
        $select_field_html   = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path . "select.html", $tag_replacement_arr);
        /** The hidden field is displayed */
        $this->GetComponent("application")->DisplayOutput($select_field_html);
    }
    
    /** 
     * Displays the hidden settings field
     * 
     * @param array $args an array containing the field type and option id
     */
    public function HiddenFieldCallback($args)
    {        
        /** The options id is fetched */
	    $options_id           = $this->GetComponent("application")->GetOptionsId("options");	
        /** The path to the plugin template folder */
        $plugin_template_path = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR . "hidden.html";
        /** The plugin prefix */
        $plugin_prefix        = $this->GetConfig("wordpress", "plugin_prefix");
        /** The id of the logged in user */
        $user_id              = $this->site_settings['user_id'];
		/** The field name */
		$field_name           = $args['field_name'];
        /** The field value is set to the saved option value. If the saved option value does not exist then it is set to the default value **/
		$field_value          = $args['default_value'];
        /** The tag replacement array is built */
        $tag_replacement_arr = array(
            array(
                "id" => $plugin_prefix . "_" . $field_name,
                "name" => $options_id . '[' . $field_name . ']',
                "value" => $field_value
            )
        );
        
        /** The settings page template is rendered */
        $hidden_field_html = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path, $tag_replacement_arr);
        /** The hidden field is displayed */
        $this->GetComponent("application")->DisplayOutput($hidden_field_html);
    }
}