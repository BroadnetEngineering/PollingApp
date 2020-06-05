import React, {Component} from 'react';

class PollComponent extends Component {
    constructor(props) {
        super(props);

        this.deletePoll = this.deletePoll.bind(this);
        this.updatePoll = this.updatePoll.bind(this);
    }

    updatePoll(option) {
        const {title, updatePoll} = this.props;

        updatePoll(title, option);
    }

    deletePoll() {
        const {title, deletePoll} = this.props;

        deletePoll(title);
    }

    renderCount(bool, count) {
        if(!bool) {return null}
        return (
            count
        )
    }

    render() {
        const {title, options, didVote} = this.props;

        return (
            <div key={title}>
                <h3>
                    {title}
                </h3>
                {options.map(option => {  
                    return (<button key={option.title} onClick={()=> this.updatePoll(option.title)}>{option.title} {this.renderCount(didVote, option.count)}</button>)})
                }
                <br/>
                <button onClick={this.deletePoll}>
                    Remove Poll
                </button>
            </div>
        );
    }
}


export default PollComponent;
