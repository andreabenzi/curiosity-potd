// Difference between TAI and UTC. This value should be
// updated each time the IERS announces a leap second.
// Last time value changed: 1 January 2017
var tai_offset = 37;

function cos(deg) {
return Math.cos(deg * Math.PI / 180);
}
function sin(deg) {
return Math.sin(deg * Math.PI / 180);
}

var d = new Date();

var millis = d.getTime();
var jd_ut = 2440587.5 + (millis / 8.64E7);
var jd_tt = jd_ut + (tai_offset + 32.184) / 86400;
var j2000 = jd_tt - 2451545.0;
var m = (19.3870 + 0.52402075 * j2000) % 360;
var alpha_fms = (270.3863 + 0.52403840 * j2000) % 360;
var e = (0.09340 + 2.477E-9 * j2000);
var pbs =
    0.0071 * cos((0.985626 * j2000 /  2.2353) +  49.409) +
    0.0057 * cos((0.985626 * j2000 /  2.7543) + 168.173) +
    0.0039 * cos((0.985626 * j2000 /  1.1177) + 191.837) +
    0.0037 * cos((0.985626 * j2000 / 15.7866) +  21.736) +
    0.0021 * cos((0.985626 * j2000 /  2.1354) +  15.704) +
    0.0020 * cos((0.985626 * j2000 /  2.4694) +  95.528) +
    0.0018 * cos((0.985626 * j2000 / 32.8493) +  49.095);
var nu_m = (10.691 + 3.0E-7 * j2000) * sin(m) +
    0.623 * sin(2 * m) +
    0.050 * sin(3 * m) +
    0.005 * sin(4 * m) +
    0.0005 * sin(5 * m) +
    pbs;
var nu = nu_m + m;
var l_s = (alpha_fms + nu_m) % 360;
var eot = 2.861 * sin(2 * l_s) - 0.071 * sin(4 * l_s) + 0.002 * sin(6 * l_s) - nu_m;
var eot_h = eot * 24 / 360;
var msd = (((j2000 - 4.5) / 1.027491252) + 44796.0 - 0.00096);
var mtc = (24 * msd) % 24;

var curiosity_lambda = 360 - 137.4;
var curiosity_sol = Math.floor(msd - curiosity_lambda / 360) - 49268;

wp.blocks.registerBlockType('brad/border-box', {
	title: 'Curiosity SOL Block',
  	icon: 'marker',
  	category: 'common',
  	attributes: {
    	content: {type: 'string'},
    	color: {type: 'string'}
  	},
 
  	edit: function(props) {
    	return React.createElement(
      		"div",
      		null,
      		React.createElement("p",null,curiosity_sol)
    	);
  	},

  	save: function(props) {
		return wp.element.createElement("p",null,curiosity_sol);
    }
});