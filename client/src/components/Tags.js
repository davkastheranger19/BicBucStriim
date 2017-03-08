import React from 'react';
import {PageHeader} from 'react-bootstrap'
import {Locs} from '../l10n'


class Tags extends React.Component {

	constructor() {
		super();
		this.locs = Locs();
	}

	render() {    
		return (
        	<div>
  				<PageHeader>{this.locs.tags}</PageHeader>		
         	</div>
		)
	}

}

export default Tags;
