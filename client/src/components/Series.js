import React from 'react';
import {PageHeader} from 'react-bootstrap'
import WithDefaults from './WithDefaults'


class Series extends React.Component {

	render() {    
		return (
			<WithDefaults>
          	{(locs) => (
	        	<div>
	  				<PageHeader>{locs.series}</PageHeader>		
	         	</div>
	         )}
	        </WithDefaults>
		)
	}

}

export default Series;
