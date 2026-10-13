// Quantum Ethic Air Quality Map — places shared by index.html and build-history.html
// Each entry: [name, latitude, longitude]
window.QE_PLACES = {
  // Volunteer communities go here as volunteers report their towns.
  volunteer: [],
  reference: [
    ["New York, NY",40.7128,-74.0060],["Los Angeles, CA",34.0522,-118.2437],["Chicago, IL",41.8781,-87.6298],
    ["Houston, TX",29.7604,-95.3698],["Phoenix, AZ",33.4484,-112.0740],["Philadelphia, PA",39.9526,-75.1652],
    ["San Jose, CA",37.3382,-121.8863],["Seattle, WA",47.6062,-122.3321],["Denver, CO",39.7392,-104.9903],
    ["Atlanta, GA",33.7490,-84.3880],["Boston, MA",42.3601,-71.0589],["Washington, DC",38.9072,-77.0369],
    ["Miami, FL",25.7617,-80.1918],["Minneapolis, MN",44.9778,-93.2650],["Salt Lake City, UT",40.7608,-111.8910]
  ]
};
// Land mask for the contiguous US: longitude ranges with land, per 2-degree latitude row (used by both grids).
window.QE_LAND = {25:[[-99,-97],[-82,-80]],27:[[-100,-97],[-83,-80]],29:[[-104,-89],[-85,-80]],31:[[-111,-81]],33:[[-118,-79]],35:[[-121,-76]],37:[[-123,-76]],39:[[-124,-75]],41:[[-124,-70]],43:[[-124,-70]],45:[[-124,-67]],47:[[-124,-68]],49:[[-123,-95]]};
