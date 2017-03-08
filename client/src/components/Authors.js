import React from 'react';
import {PageHeader} from 'react-bootstrap'
import {Locs} from '../l10n'


class Authors extends React.Component {

	constructor() {
		super();
		this.locs = Locs();
	}

	render() {    
		return (
        	<div>
  				<PageHeader>{this.locs.authors}</PageHeader>		
         	</div>
		)
	}

}

export default Authors;
