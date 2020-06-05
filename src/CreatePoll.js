import React, {Component} from 'react';

class CreatePoll extends Component {
    constructor(props) {
        super(props);

        this.onSubmit = this.onSubmit.bind(this);
    }

    onSubmit(e) {
        e.preventDefault();
        
        const {addPoll} = this.props

        addPoll(this.titleInput.value, this.opt1Input.value, this.opt2Input.value);
    }

    render() {
        return (
            <form onSubmit={this.onSubmit}>
                <h3>
                    Create Poll
                </h3>
                <input placeholder="Title" ref={titleInput => this.titleInput = titleInput}/>
                <input placeholder="Option 1" ref={opt1Input => this.opt1Input = opt1Input}/>
                <input placeholder="Option 2" ref={opt2Input => this.opt2Input = opt2Input}/>
                <button>Create</button>
            </form>
        );
    }
}


export default CreatePoll;
