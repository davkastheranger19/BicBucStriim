import React from 'react';
import {PageHeader} from 'react-bootstrap'
import WithDefaults from './WithDefaults'


class Titles extends React.Component {

	render() {    
		return (
			<WithDefaults>
			{(locs) => (
	        	<div>
	  				<PageHeader>{locs.titles}</PageHeader>		
	        		<p>{window.navigator.userAgent}</p>
	         	</div>
	         )}
	  		</WithDefaults>
		)
	}

}

export default Titles;
