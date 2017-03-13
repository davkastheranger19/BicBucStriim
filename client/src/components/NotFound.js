import React from 'react';
import {PageHeader} from 'react-bootstrap'
import WithDefaults from './WithDefaults'


class NotFound extends React.Component {

	render() {
		return (
		    <WithDefaults>
          	{(locs) => (
	  			<div>
	  				<PageHeader>{this.locs.not_found1}</PageHeader>
	  				<p>{this.locs.not_found2}</p>
	  			</div>
	  		)}
	  		</WithDefaults>
		)
	}
}

export default NotFound;
