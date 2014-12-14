
var APIURL = "http://localhost/crm/";

var utilsGeneral = function ($http) {

  return {

	    GetTextIcon: function GetTextIcon(extension) {
	    	switch(extension) {
			    case "pdf":
			        ext = "pdf";
			        break;
			    case "doc":
			        ext = "word";
			        break;
			    case "docx":
			        ext = "word";
			        break;
			    case "xls":
			        ext = "excel";
			        break;
			    case "xlsx":
			        ext = "excel";
			        break;
			    case "jpg":
			        ext = "image";
			        break;
			    case "png":
			        ext = "image";
			        break;
			    case "jpeg":
			        ext = "image";
			        break;
			    case "zip":
			        ext = "zip";
			        break;
			    case "rar":
			        ext = "zip";
			        break;
			    case "gz":
			        ext = "zip";
			        break;
			    default:
			        ext = "file";
			}
	      return ext;
	    },
	}
}
//Definimos los servicios
angular.module('bidef.common', [

])
.factory('utilsGeneral', utilsGeneral)