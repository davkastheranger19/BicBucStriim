import React from 'react';
import {PageHeader} from 'react-bootstrap'
import {Locs} from '../l10n'


class NotFound extends React.Component {

	constructor() {
		super();
		this.locs = Locs();
	}

	render() {
		return (
  			<div>
  				<PageHeader>{this.locs.not_found1}</PageHeader>
  				<p>{this.locs.not_found2}</p>
  			</div>
		)
	}
}

export default NotFound;
