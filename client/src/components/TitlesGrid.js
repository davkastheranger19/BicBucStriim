import React from 'react';
import {PageHeader, Grid, Row, Col, Image} from 'react-bootstrap'
import {Link} from 'react-router-dom'

import {Locs} from '../l10n'

class TitlesGrid extends React.Component {


	render() {
		return (
			<Grid>
				<Row className="show-grid">
					{this.props.titleData.map((tile) => (
						<Link to={`/titles/${tile.id}`}>
							<Col xs="6" sm="4" md="4" lg="4">
							  <Image className="title" src={tile.img}/>
							  <div className="x0">
							  	<div className="x1">
						    		<div className="x2" >{tile.title}</div>
						    		<div className="x3">
						      			<span>by <b>{tile.author}</b></span>
						    		</div>
						  		</div>
							  </div>
							</Col>
						</Link>
					))}
				</Row>
			</Grid>
		)
	}
}

export default TitlesGrid;
