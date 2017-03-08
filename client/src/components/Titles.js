import React from 'react';
import {PageHeader} from 'react-bootstrap'
import {Locs} from '../l10n'


class Titles extends React.Component {

	constructor() {
		super();
		this.locs = Locs();
	}

	render() {    
		return (
        	<div>
  				<PageHeader>{this.locs.titles}</PageHeader>		
        		<p>{window.navigator.userAgent}</p>
         	</div>
		)
	}

}

export default Titles;
