import React from 'react';
import {PageHeader} from 'react-bootstrap'
import WithDefaults from './WithDefaults'


class Tags extends React.Component {

	render() {    
		return (
			<WithDefaults>
			{(locs) => (
	        	<div>
	  				<PageHeader>{locs.tags}</PageHeader>		
	         	</div>
	         )}
	  		</WithDefaults>
		)
	}

}

export default Tags;
