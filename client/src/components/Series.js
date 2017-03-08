import React from 'react';
import {PageHeader} from 'react-bootstrap'
import {Locs} from '../l10n'


class Series extends React.Component {

	constructor() {
		super();
		this.locs = Locs();
	}

	render() {    
		return (
        	<div>
  				<PageHeader>{this.locs.series}</PageHeader>		
         	</div>
		)
	}

}

export default Series;
