import React from 'react';
import {PageHeader, Grid, Row, Col, Image} from 'react-bootstrap'
import {Link} from 'react-router-dom'

import {Locs} from '../l10n'

class TitleDetail extends React.Component {
	constructor() {
	    super()
	    this.locs = Locs()
	    this.state = {
	    }
  	}

  	render() {
  		return (
  			<div>
  				<PageHeader>TitleDetail</PageHeader>		
  			</div>
  		)
  	}
}

export default TitleDetail
