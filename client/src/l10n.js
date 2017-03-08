import LocalizedStrings from 'react-localization'

export function Locs() { 
	return new LocalizedStrings({
	      en:{
	        home:"Home",
	        titles:"Titles",
	        authors:"Authors",
	        tags:"Tags",
	        series:"Series",
	        most_recent: "Most recent"
	      },
	      de: {
	        home:"Start",
	        titles:"Bücher",
	        authors:"Autoren",
	        tags:"Schlagworte",
	        series:"Serien",
	        most_recent: "Die neuesten"
	      }
	    })
}
